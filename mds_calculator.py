import numpy
import mds

cfd_mds_data = file('../../cfd_mds_data/datastore.txt').read().split('\n')

NUMFACES = 158

difference_matrix = numpy.zeros(shape = (NUMFACES, NUMFACES))
comparison_count = numpy.zeros(shape = (NUMFACES, NUMFACES))

average_similarity = reduce(lambda a, b: a + b, map(lambda row: float(row.split('\t')[4]), cfd_mds_data[:-1])) / (len(cfd_mds_data) - 1)
imageLocations = {}

for row in cfd_mds_data[:-1]:
    cols = row.split('\t')
    f1 = int(cols[0])
    f2 = int(cols[1])
    imageLocations[f1] = cols[2]
    imageLocations[f2] = cols[3]
    similarity = float(cols[4])
    difference_matrix[f1][f2] = (comparison_count[f1][f2] * difference_matrix[f1][f2] + similarity) / (difference_matrix[f1][f2] + 1)

for y, row in enumerate(difference_matrix):
    for x, cell in enumerate(row):
        if cell == 0:
            if x != y:
                difference_matrix[y][x] = average_similarity
            else:
                difference_matrix[y][x] = 7
        difference_matrix[y][x] -= 7
        difference_matrix[y][x] *= -1

positions2d, eigs2d = mds.mds(difference_matrix, 2)
positions1d, eigs1d = mds.mds(difference_matrix, 1)

locations2d = file('2dlocations.txt', 'w+')
locations1d = file('1dlocations.txt', 'w+')
for index, position in enumerate(positions2d):
    locations2d.write('"{0}"\t{1}\t{2}\n'.format(imageLocations[index], position[0], position[1]))
for index, position in enumerate(positions1d):
    locations1d.write('"{0}"\t{1}\n'.format(imageLocations[index], position[0]))

